<?php
class Updater {

    public $db, $yesterday, $today, $mrHandler, $maxUsers = 10, $currentRow = 0, $usersInfo = array();

    public function __construct( Database $db ) {
        $this->db        = $db;
        $this->yesterday = strtotime( 'yesterday 00:00:00' );
        $this->today     = strtotime( 'today 00:00:00' );
        $this->mrHandler = new MultiRequest_Handler();

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "gzip",       // handle all encodings
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12)", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
            CURLOPT_TIMEOUT        => 10,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $this->mrHandler->setConnectionsLimit( 5 );
        $this->mrHandler->requestsDefaults()->addCurlOptions( $options );
        // $this->mrHandler->onRequestComplete( array( $this, 'debug' ) );
        $this->mrHandler->onRequestComplete( array( $this, 'prepareUser' ) );
    }

    /**
     * Start the updater
     */
    public function run() {
        echo "{$this->currentRow}, {$this->maxUsers}" . PHP_EOL;
        $this->db->query( "SELECT `id`, `username` FROM `users` ORDER BY `id` ASC LIMIT {$this->currentRow}, {$this->maxUsers}" )->execute();
        if ( $this->db->getTotalRows() ) {
            $urls = array();
            foreach ( $this->db->fetchAll() as $user ) {
                $urls[] = 'http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player=' . $user['username'];
            }

            foreach ( $urls as $url ) {
                $request = new MultiRequest_Request( $url );
                $this->mrHandler->pushRequestToQueue( $request );
            }
            $this->mrHandler->start();
        }
    }

    /**
     * Called by the MultiRequest callback to prepare a use for updating
     */
    public function prepareUser( MultiRequest_Request $request, MultiRequest_Handler $handler ) {
        $username = preg_replace( '#http://services.runescape.com/m=hiscore_oldschool/index_lite.ws\?player=(.*)#', '$1', $request->getUrl() );

        $this->usersInfo[$username] = array( 'username' => $username, 'id' => $this->getIdByUser( $username ) );
        $this->usersInfo[$username]['skillInfo'] = $request->getCode() == 200 ? $this->formatUserInfo( $request->getContent() ) : null ;

        if ( $handler->getActiveRequestsCount() == 0 ) {
            $this->updateUsers();
        }
    }

    public function debug( MultiRequest_Request $request, MultiRequest_Handler $handler ) {
        echo 'Request complete: ' . $request->getUrl() . ' Code: ' . $request->getCode() . ' Time: ' . $request->getTime() . PHP_EOL;
        echo 'Requests in waiting queue: ' . $handler->getRequestsInQueueCount() . PHP_EOL;
        echo 'Active requests: ' . $handler->getActiveRequestsCount() . PHP_EOL;
    }

    /**
     * Runs the updater for users in the userInfo array
     */
    private function updateUsers() {
        foreach ( $this->usersInfo as $user ) {
            if ( is_null( $user['skillInfo'] ) ) {
                $this->db->query( 'UPDATE `users` SET `last_check` = ?, `flagged` = 1 WHERE `id` = ?' )->bind( 1, time() )->bind( 2, $user['id'] )->execute();
                continue;
            }

            $this->updateYesterday( $user );
            $this->newDay( $user );
        }
        $this->usersInfo = array();
        $this->currentRow += $this->maxUsers;
        $this->run();
    }

    /**
     * Updates the database for 'yesterdays' date
     *
     * @param array   $user Array holding all of the users information
     */
    private function updateYesterday( $user ) {
        $this->db->query( 'SELECT `id` FROM `day_rank` WHERE `user_id` = ? AND `date` = ?' )->bind( 1, $user['id'] )->bind( 2, $this->yesterday )->execute();
        if ( $this->db->getTotalRows() ) {
            $this->db->query( 'SELECT * FROM `day_stats` WHERE `user_id` = ? AND `date` = ?' )->bind( 1, $user['id'] )->bind( 2, $this->yesterday )->execute();
            $yesterdayStats = $this->db->fetch();
            $updateStrings = array( 'ranks' => array(), 'levels' => array(), 'xp' => array() );
            for ( $i = 0; $i < 24; $i++ ) {
                $json                      = json_decode( $yesterdayStats['' . $i . ''] );
                $updateStrings['ranks'][]  = "`{$i}` = " . ( $user['skillInfo'][$i]['rank'] - $json->{'rank'} );
                $updateStrings['levels'][] = "`{$i}` = " . ( $user['skillInfo'][$i]['level'] - $json->{'level'} );
                $updateStrings['xp'][]     = "`{$i}` = " . ( $user['skillInfo'][$i]['xp'] - $json->{'xp'} );
            }
            $this->db->query( "UPDATE `day_rank` SET " . implode( ",", $updateStrings['ranks'] ) ." WHERE `user_id` = ? AND `date` = ?" )->bind( 1, $user['id'] )->bind( 2, $this->yesterday )->execute();
            $this->db->query( "UPDATE `day_level` SET " . implode( ",", $updateStrings['levels'] ) ." WHERE `user_id` = ? AND `date` = ?" )->bind( 1, $user['id'] )->bind( 2, $this->yesterday )->execute();
            $this->db->query( "UPDATE `day_xp` SET " . implode( ",", $updateStrings['xp'] ) ." WHERE `user_id` = ? AND `date` = ?" )->bind( 1, $user['id'] )->bind( 2, $this->yesterday )->execute();
        }
    }

    private function newDay( $user ) {
        $this->db->query( "SELECT `user_id` FROM `day_stats` WHERE `user_id` = ? AND `date` = ?" )->bind( 1, $user['id'] )->bind( 2, $this->today );
        if ( $this->db->getTotalRows() == 0 ) {
            $insertString = "";
            for ( $i=0; $i < count( $user['skillInfo'] ); $i++ ) {
                $insertString .= "'" . json_encode( $user['skillInfo'][$i] ) . "', ";
            }
            $insertString = substr( $insertString, 0, -2 );
            $this->db->query( "INSERT INTO `day_stats` (`user_id`, `date`, `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23`) VALUES ({$user['id']}, {$this->today}, {$insertString})" )->execute();
            $this->db->query( "INSERT INTO `day_rank` (`user_id`, `date`) VALUES (?, ?)" )->bind( 1, $user['id'] )->bind( 2, $this->today )->execute();
            $this->db->query( "INSERT INTO `day_level` (`user_id`, `date`) VALUES (?, ?)" )->bind( 1, $user['id'] )->bind( 2, $this->today )->execute();
            $this->db->query( "INSERT INTO `day_xp` (`user_id`, `date`) VALUES (?, ?)" )->bind( 1, $user['id'] )->bind( 2, $this->today )->execute();
            $this->db->query( "UPDATE `users` SET `last_check` = ?, `flagged` = 0 WHERE `id` = ?" )->bind( 1, time() )->bind( 2, $user['id'] )->execute();
        }
    }

    /**
     * Format the page content from runescape hiscores into an array
     *
     * @param string  $content The content of the hiscores page
     * @return array          Array holding every skills rank, level and xp
     */
    private function formatUserInfo( $content ) {
        $skills = explode( "\n", $content );
        $info   = array(); // Stores ranks, levels and xp

        if (count($skills) < 24) {
            return null;
        }

        for ( $i = 0; $i < 24; $i++ ) {
            $skillBits = explode( ",", $skills[$i] );
            $info[] = array(
                'rank'  => $skillBits[0],
                'level' => $skillBits[1],
                'xp'    => $skillBits[2]
            );
        }
        return $info;
    }

    /**
     * Get userid by username
     *
     * @param string  $user The username of the user
     * @return mixed       false if the user doesn't exist else the user id
     */
    private function getIdByUser( $user ) {
        $this->db->query( 'SELECT `id` FROM `users` WHERE `username` = ?' )->bind( 1, $user )->execute();
        if ( $this->db->getTotalRows() ) {
            $result = $this->db->fetch();
            return $result['id'];
        } else {
            return false;
        }
    }

}

require 'config.php';
require 'Database.php';

$startTime = time();

$updater = new Updater( new Database( $dbInfo ) );
$updater->run();
echo PHP_EOL . "Execution time: " . ( time() - $startTime ) . ", Query Count: " . count( $updater->db->queries ) . PHP_EOL;

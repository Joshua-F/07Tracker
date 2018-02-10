<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getPlayerGraphData() {
        $toReturn = array();
        // $query = $this->db->query('SELECT `time`, `count`, `rs_count`, `total_count` FROM (SELECT `time`, `count`, `rs_count`, `total_count` FROM `player_counter` ORDER BY `id` DESC LIMIT 50000) as t;');
        $query = $this->db->query('SELECT `time`, `count`, `rs_count`, `total_count` FROM `player_counter` ORDER BY `time` desc LIMIT 50000');
        foreach ($query->result() as $result) {
            $time = $result->time * 1000;
            $totalCount = (int) $result->total_count < 1 ? "null" : (int) $result->total_count;
            $rsCount = (int) $result->total_count < 1 || $result->rs_count < 2 ? "null" : (int) $result->rs_count ;
            $toReturn['totalCount'][] = "[new Date({$time}), {$totalCount}]";
            $toReturn['rsCount'][]    = "[new Date({$time}), {$rsCount}]";
            $toReturn['07count'][]    = "[new Date({$time}), {$result->count}]";
        }

        $toReturn['totalCount'] = array_reverse($toReturn['totalCount']);
        $toReturn['rsCount'] = array_reverse($toReturn['rsCount']);
        $toReturn['07count'] = array_reverse($toReturn['07count']);

        return $toReturn;
    }

    public function getPlayerCountData() {
        $query = $this->db->query("SELECT MAX(`count`) as `maxCount`, MAX(`rs_count`) as `maxRSCount`, MAX(`total_count`) as `maxTotalCount`, MIN(`count`) as `minCount` FROM `player_counter`");
        $toReturn = $query->row_array();

        $query = $this->db->query("SELECT MIN(`rs_count`) as `minRSCount`, MIN(`total_count`) as `minTotalCount` FROM `player_counter` WHERE `rs_count` != 0");
        $result = $query->row();
        $toReturn['minRSCount'] = $result->minRSCount;
        $toReturn['minTotalCount'] = $result->minTotalCount;

        return $toReturn;
    }

}

/* End of file Page.php */
/* Location: ./application/models/Page.php */
## Introduction
07Tracker was a project built to help
[Oldschool Runescape](http://oldschool.runescape.com/) players track
their individual skills from the game. It was written in PHP using
[Codeigniter](https://www.codeigniter.com/userguide2) 2. The official site is
[07Tracker.com](https://07tracker.com) but has since been discontinued and kept
up only to serve signatures.

## Features
- Individual skill tracking
- Daily Top 50 overall or specific skill
- Top 50 History to be able to see the Top 50 for other dates
- Personal profile page with the gains for the day, a graph of overall
increase, and a way to view a persons history
- Signature generation
- RuneScape player count tracking for [RuneScape](http://www.runescape.com) and
Oldschool Runescape.

## Notes
- The source code was originally written in 2013 and received very few updates
since.
- The table structures I used were questionable.
  - Storing day starting stats as a `JSON` string in `day_stats` was not the best of ideas.
- Codeigniter 2 was pretty outdated even in 2013.
- Noteworthy files/directories
  - The database scheme is in [07tracker.sql](07tracker.sql).
  - The apache config is in [.htaccess](.htaccess).
  - The nginx config is in [nginx.conf](nginx.conf).
  - The config files are in [application/config](application/config).
  - Things that should be ran with a cronjob are in
  [tracker-updater](tracker-updater).
- There are URLs and paths hardcoded that you would need to manually change.
- Probably some other "gotchas" and this is only here for reference.

## Contributing
I will not be actively maintaining this project so there is no reason to
contribute back to it.

## License
The 07Tracker source code is open-sourced software licensed under the
[MIT license](http://opensource.org/licenses/MIT).
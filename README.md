# gyp-scraper-db
A Web Scraper for eltern-portal.org.

Collects data about the daily schedule of students, stores it temporarily in a Database and displays it on a website. 
Script gets executed regularly via cron, to keep the data up to date.

The script needs a few modules: robobrwoser, pandas, sqlalchemy, pymysql, datetime and bs4 (BeautifulSoup4). If not installed, simply install via pip.

The credentials for the script are stored in an external conf.py file.

index.php uses a config.ini file, located at '../../priv/cofig.ini'. (index.php is located at '<rootdir-of-website>/vertretungsplan/index.php')

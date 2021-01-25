import werkzeug
werkzeug.cached_property = werkzeug.utils.cached_property
from robobrowser import RoboBrowser
import conf
import pandas as pd
from sqlalchemy import create_engine
from datetime import datetime
from bs4 import BeautifulSoup

br = RoboBrowser()
br.parser = "html.parser"

br.open('https://gypenz.eltern-portal.org/service/vertretungsplan')
form = br.get_form()
form["username"] = conf.GYP_USER
form['password'] = conf.GYP_PASSWORD
br.submit_form(form)
br.open('https://gypenz.eltern-portal.org/service/vertretungsplan')
src = str(br.parsed())
res = str(br.select('table'))
raw = pd.read_html(res)
raw.pop(3)
raw.pop(0)

df_to = pd.DataFrame(raw[0])
df_tm = pd.DataFrame(raw[1])

soup = BeautifulSoup(src, 'html.parser')
dates = soup.find_all('div', class_='list bold full_width text_center')
date_to = str(dates[0])
date_tm = str(dates[1])
date_to = date_to.replace('<div class="list bold full_width text_center">', '')
date_to = date_to.replace('</div>', '')
date_tm = date_tm.replace('<div class="list bold full_width text_center">', '')
date_tm = date_tm.replace('</div>', '')

data = {'datum_plan_heute': [date_to], 'datum_plan_morgen': [date_tm]}
daten = pd.DataFrame (data, columns = ['datum_plan_heute', 'datum_plan_morgen'])

if df_to.iat[0,0] == "Keine Vertretungen für die 12Q":
    daten['keine_v_heute'] = [True]
else:
    daten['keine_v_heute'] = [False]

if df_tm.iat[0,0] == "Keine Vertretungen für die 12Q":
    daten['keine_v_morgen'] = [True]
else:
    daten['keine_v_morgen'] = [False]
    
new_header = df_tm.iloc[0]
df_tm = df_tm[1:]
df_tm.columns = new_header
new_header = df_to.iloc[0]
df_to = df_to[1:]
df_to.columns = new_header

updated_at = []
time = datetime.now().strftime("%X %d-%m-%y")
for i in range(len(df_to.index)):
    updated_at.append(time)

df_to['updated_at'] = updated_at

updated_at = []
for i in range(len(df_tm.index)):
    updated_at.append(time)

df_tm['updated_at'] = updated_at


engine = create_engine("mysql+pymysql://{user}:{pw}@{ip}/{db}".format(user=conf.SQL_USER,pw=conf.SQL_PASSWORD, ip=conf.SERVER_IP, db="scraper"))
df_to.to_sql('plan_heute', con = engine, if_exists = 'replace', chunksize = 1000)
df_tm.to_sql('plan_morgen', con = engine, if_exists = 'replace', chunksize = 1000)
daten.to_sql('pläne_daten', con = engine, if_exists = 'replace', chunksize = 1000)

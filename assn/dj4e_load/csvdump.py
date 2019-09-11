import csv

fh = open('whc-sites-2018-small.csv')
rows = csv.reader(fh)
i = 0
for row in rows:
    if len(row[0]) < 1 : continue
    print(row[0])
    i = i + 1
    if i > 5 : break


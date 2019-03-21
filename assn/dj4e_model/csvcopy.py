import csv

# https://stackoverflow.com/questions/20078816/replace-non-ascii-characters-with-a-single-space
# fh = open('whc-sites-2018-small.csv')

fhand = open('whc-sites-2018-small.csv');
reader = csv.reader(fhand)
fo = open('out.csv','w');
writer = csv.writer(fo,  delimiter=',')

for row in reader:
    print(row)
    for i in range(len(row)) :
        old = row[i]
        new = ''.join([ch if ord(ch) < 128 else ' ' for ch in old])
        row[i] = new
    writer.writerow(row)


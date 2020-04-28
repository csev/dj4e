import json

j = json.load(open("lessons.json"))
modules = j['modules']
for module in modules:
    title = module.get('title')
    slides = module.get('slides')
    refs = module.get('references')
    print()
    if title: print(title)
    if slides: 
        print('Slides: '+title+'\r')
        print("https://www.dj4e.com/"+slides+'\r')
    if refs : 
        for ref in refs :
            rt = ref.get('title')
            ru = ref.get('href')
            if ru and not ru.startswith('http') : 
                ru = 'https://www.dj4e.com/' + ru
            if rt and ru : 
                print(rt)
                print(ru)
                # print('<a href="'+ru+'">'+rt+'</a>')




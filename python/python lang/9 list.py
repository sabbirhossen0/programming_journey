#list  define

#List is a collection which is ordered and changeable. Allows duplicate members.

agelist=[10,20,30,40,50,60]

#single value
print(agelist[1])

#multiple value 
for x in agelist:
    print(x)

# display full list
print(agelist)    

# add list items

agelist.append(13)

print(agelist) #[10, 20, 30, 40, 50, 60, 13]

#add item using insert

agelist.insert(1,80) #index number,value 
print(agelist) #[10, 80, 20, 30, 40, 50, 60, 13]

newagelist=agelist

# remove specific 
agelist.pop(0)

print(agelist)

#list shorting  
  #asending
agelist.sort(reverse=False)
print(agelist)

#decending
agelist.sort(reverse=False)
print(agelist)
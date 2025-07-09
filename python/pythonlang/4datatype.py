print("PYthon Data Type")

"""
Python has the following data types built-in by default, 

Text Type:	str
Numeric Types:	int, float, complex
Sequence Types:	list, tuple, range
Mapping Type:	dict
Set Types:	set, frozenset
Boolean Type:	bool
Binary Types:	bytes, bytearray, memoryview
None Type:	NoneType 

"""

name="sabbir" #str datatype
id=499 #integer
cgpa=3.95 #float
froutlist = ["apple", "banana", "cherry"]	#list	
frouttuple = ("apple", "banana", "cherry")	#tuple	
x = range(6)    #	range	
persiondict = {"name" : "John", "age" : 36}	#dict	
froutset = {"apple", "banana", "cherry"}	#set	
froutfset = frozenset({"apple", "banana", "cherry"})	#frozenset	
ybool = True #bool

print(type(name), name)
print(type(id), id)
print(type(cgpa), cgpa)
print(type(froutlist), froutlist)
print(type(frouttuple), frouttuple)
print(type(x), x)
print(type(persiondict), persiondict)
print(type(froutset), froutset)

print(type(froutfset), froutfset)
print(type(ybool),ybool)
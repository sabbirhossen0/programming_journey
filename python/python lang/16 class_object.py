

# class define
class value:
    x=10
    def sum(a,b):
        return a+b

# object define
object1  =value

print(object1.x)
object1.x=20
print(object1.x)

# Sum function access
sum1=object1.sum(10,20)
print(sum1)


# self parameter are used to uniquely identify objects.
class Person:
  def __init__(self, name, age):
    self.name = name
    self.age = age

# object name must be unique

p1 = Person("sabbir", 21)
p2 = Person("sajjad", 18)
print(p1.name)
print(p1.age)
print(p2.name)
print(p2.age)
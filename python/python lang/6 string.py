# You can assign a multiline string to a variable by using three quotes:

a = '''Lorem ipsum dolor sit amet,
consectetur adipiscing elit,
sed do eiusmod tempor incididunt
ut labore et dolore magna aliqua.'''
print(a)

#or

a = """Lorem ipsum dolor sit amet,
consectetur adipiscing elit,
sed do eiusmod tempor incididunt
ut labore et dolore magna aliqua."""
print(a)

b = "Hello programmer!"

# String slicing

#the characters from position 2 to position 5 (not included):
print(b[2:5])
# that means 0 index start  and <10 
print(b[:10])

#negative 
print(b[-10:-2])

#String Modifing 

u=" Hello programmer "
print(u.upper())
print(u.lower())
print(u.strip())

#String Concatenation

title="English"
teacher="muhid"
print(title +" "+ teacher)

#string formate 
age=20.3231
txt=f"your age {age}"
print(txt)
txt=f"your age {age:.2f}"
print(txt)


#Escape Character


# \	Single Quote	
# \\	Backslash	
# \n	New Line	
# \r	Carriage Return	
# \t	Tab	
# \b	Backspace	
# \f	Form Feed	
# \ooo	Octal value	
# \xhh	Hex value



print("My name is  \"Sabbir\" ")


# Available built-in  string method follow python doc  
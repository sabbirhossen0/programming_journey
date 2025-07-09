class Teacher:
    def __init__(self):
        self.id = 10
        self.name = "Sabbir"

    def display(self):
        value = f"{self.id} and {self.name}"
        print(value)

class student(Teacher):
      pass

teacher1 = Teacher()  # Create an instance of the class
teacher1.display()  # Call the method correctly

inh=student()
inh.display()

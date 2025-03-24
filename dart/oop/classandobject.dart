// Class definition
class Person {
  // Properties (Variables)
  String name = '';
  int age = 0;

  // Method (Function)
  void introduce() {
    print('Hi, I am $name and I am $age years old.');
  }
}

void main() {
  // Creating an object (instance) of Person
  Person person1 = Person();
  person1.name = 'Sabbir';
  person1.age = 25;

  // Accessing method
  person1.introduce();

  // You can create more objects
  Person person2 = Person();
  person2.name = 'Helim';
  person2.age = 30;

  person2.introduce();
}

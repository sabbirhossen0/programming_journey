void main() {
  // 1. Basic Function (No Return, No Parameters)
  greet();

  // 2. Function With Parameters
  greetUser('Sabbir');

  // 3. Function With Return Type
  int sumResult = add(10, 20);
  print('Sum result: $sumResult');

  // 4. Arrow Function
  int product = multiply(4, 5);
  print('Product: $product');

  // 5. Optional Positional Parameters
  greetOptional('Sabbir');
  greetOptional('Sabbir', 'Mr.');

  // 6. Optional Named Parameters with Default Values
  describeUser(name: 'Sabbir', age: 25);
  describeUser(); // Uses default values

  // 7. Anonymous Function
  List<int> numbers = [1, 2, 3];
  numbers.forEach((number) {
    print('Anonymous Function - Number: $number');
  });

  // 8. Higher-Order Function (Passing Function as Parameter)
  calculator(6, 2, add);
  calculator(6, 2, subtract);

  // 9. Function Returning a Function
  var doubleIt = multiplier(2);
  print('Double of 7 is: ${doubleIt(7)}');

  // 10. Recursive Function
  int fact = factorial(5);
  print('Factorial of 5 is: $fact');

  // 11. Asynchronous Function Example (Future)
  fetchData().then((data) {
    print('Fetched data: $data');
  });

  print('Main function done.');
}

// 1. Basic Function
void greet() {
  print('Hello from greet()!');
}

// 2. Function With Parameters
void greetUser(String name) {
  print('Hello, $name!');
}

// 3. Function With Return Type
int add(int a, int b) {
  return a + b;
}

// 4. Arrow Function
int multiply(int a, int b) => a * b;

// 5. Optional Positional Parameters
void greetOptional(String name, [String? title]) {
  if (title != null) {
    print('Hello, $title $name');
  } else {
    print('Hello, $name');
  }
}

// 6. Optional Named Parameters With Default Values
void describeUser({String name = 'Unknown', int age = 0}) {
  print('User Name: $name, Age: $age');
}

// 7. Anonymous Function (Already used in main inside forEach)

// 8. Higher-Order Function
void calculator(int a, int b, int Function(int, int) operation) {
  int result = operation(a, b);
  print('Calculator Result: $result');
}

int subtract(int a, int b) => a - b;

// 9. Function Returning Another Function
Function multiplier(int factor) {
  return (int number) => number * factor;
}

// 10. Recursive Function
int factorial(int n) {
  if (n == 0) return 1;
  return n * factorial(n - 1);
}

// 11. Asynchronous Function Example
Future<String> fetchData() async {
  await Future.delayed(Duration(seconds: 2)); // Simulate network delay
  return 'Data Loaded!';
}

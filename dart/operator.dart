void main() {
  // 1. Arithmetic Operators
  int a = 10;
  int b = 3;

  print('Arithmetic Operators:');
  print('a + b = ${a + b}'); // 13
  print('a - b = ${a - b}'); // 7
  print('a * b = ${a * b}'); // 30
  print('a / b = ${a / b}'); // 3.333...
  print('a ~/ b = ${a ~/ b}'); // 3
  print('a % b = ${a % b}'); // 1

  print('\n');

  // 2. Assignment Operators
  print('Assignment Operators:');
  int c = 5;
  print('c = $c'); // 5
  
  c += 2;
  print('c += 2 -> $c'); // 7
  
  c -= 1;
  print('c -= 1 -> $c'); // 6
  
  c *= 3;
  print('c *= 3 -> $c'); // 18
  
  c ~/= 2;
  print('c ~/= 2 -> $c'); // 9
  
  c %= 4;
  print('c %= 4 -> $c'); // 1

  print('\n');

  // 3. Relational Operators
  print('Relational Operators:');
  print('a == b -> ${a == b}'); // false
  print('a != b -> ${a != b}'); // true
  print('a > b -> ${a > b}');   // true
  print('a < b -> ${a < b}');   // false
  print('a >= b -> ${a >= b}'); // true
  print('a <= b -> ${a <= b}'); // false

  print('\n');

  // 4. Logical Operators
  print('Logical Operators:');
  bool x = true;
  bool y = false;
  
  print('x && y -> ${x && y}'); // false
  print('x || y -> ${x || y}'); // true
  print('!x -> ${!x}');         // false

  print('\n');

  // 5. Bitwise Operators
  print('Bitwise Operators:');
  int p = 5;  // 0101
  int q = 3;  // 0011
  
  print('p & q -> ${p & q}'); // 1 (0001)
  print('p | q -> ${p | q}'); // 7 (0111)
  print('p ^ q -> ${p ^ q}'); // 6 (0110)
  print('~p -> ${~p}');       // -6 (two's complement)
  print('p << 1 -> ${p << 1}'); // 10 (1010)
  print('p >> 1 -> ${p >> 1}'); // 2 (0010)

  print('\n');

  // 6. Type Test Operators
  print('Type Test Operators:');
  var name = 'Dart';
  
  print('name is String -> ${name is String}'); // true
  print('name is! int -> ${name is! int}');     // true

  print('\n');

  // 7. Conditional Operators (Ternary and Null-aware)
  print('Conditional Operators:');
  
  int age = 20;
  String result = age >= 18 ? 'Adult' : 'Minor';
  print('Ternary: $result'); // Adult
  
  String? username;
  String displayName = username ?? 'Guest';
  print('Null-aware: $displayName'); // Guest

  print('\n');

  // 8. Cascade Operator (..)
  print('Cascade Operator:');
  var sb = StringBuffer()
    ..write('Hello')
    ..write(' ')
    ..write('Dart!');
  
  print(sb.toString()); // Hello Dart!
}

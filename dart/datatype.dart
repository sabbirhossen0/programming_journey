void main() {
  // Numbers
  int id = 1;
  double rating = 4.5;

  // Text
  String name = 'Flutter';

  // Boolean
  bool isAvailable = true;

  // Collections
  List<String> skills = ['Dart', 'Flutter'];
  Set<int> numbers = {1, 2, 3}; // Unordered, unique elements
  Map<String, int> scores = {'Math': 90, 'English': 85};

  // Dynamic type
  dynamic anything = 'I can be anything';
  anything = 100; // Now it's an int

  // Object type
  Object something = 'Object type'; // Base type for all Dart objects
  
  // Null
  Null noValue = null;

  // Enum
  enum Status { active, inactive, pending }
  var userStatus = Status.active;

  // Function type
  Function greet = () => print('Hello from a function!');

  print('ID: $id');
  print('Rating: $rating');
  print('Name: $name');
  print('Available: $isAvailable');
  print('Skills: $skills');
  print('Numbers: $numbers');
  print('Scores: $scores');
  print('Dynamic: $anything');
  print('Object: $something');
  print('Null: $noValue');
  print('Status: $userStatus');
  
  greet(); // Calling function
}

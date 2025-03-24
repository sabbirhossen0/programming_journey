void main() {
  for (int i = 1; i <= 5; i++) {
    if (i == 3) {
      print('Breaking at i = $i');
      break; // Stops the loop when i is 3
    }
    print('i = $i');
  }
}

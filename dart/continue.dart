void main() {
  for (int i = 1; i <= 5; i++) {
    if (i == 3) {
      print('Skipping i = $i');
      continue; // Skips when i is 3
    }
    print('i = $i');
  }
}

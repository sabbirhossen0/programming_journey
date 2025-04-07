class Car {
  String brand;
  int year;

  // Constructor
  Car(this.brand, this.year);

  void displayInfo() {
    print('Brand: $brand, Year: $year');
  }
}

void main() {
  Car car1 = Car('Toyota', 2020);
  car1.displayInfo();

  Car car2 = Car('Tesla', 2023);
  car2.displayInfo();

  print("Wellcome back");
}

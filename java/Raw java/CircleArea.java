import java.util.Scanner;

class Circle {
    float R0, R1;

    void setRadius(float a, float b) {
        R0 = a;
        R1 = b;
    }

    void printArea() {
        double r0a = Math.PI * R0 * R0;
        double r1a = Math.PI * R1 * R1;
        double shadedArea = r1a - r0a;
        System.out.println("Shaded area between the two circles is: " + shadedArea);
    }
}

public class CircleArea {
    public static void main(String[] args) {

        Circle c1 = new Circle();

        Scanner sc = new Scanner(System.in);

        System.out.print("Enter radius R0 (inner circle): ");
        float r0 = sc.nextFloat();

        System.out.print("Enter radius R1 (outer circle): ");
        float r1 = sc.nextFloat();

        if (r1 > r0) {
            c1.setRadius(r0, r1);
            c1.printArea();
            System.out.println("Congratulations Sir! The program ran successfully.");
        } else {
            System.out.println("Sir! Outer radius R1 should be greater than inner radius R0.");
        }
        sc.close();
    }
}

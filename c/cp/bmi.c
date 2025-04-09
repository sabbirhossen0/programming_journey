#include <stdio.h>

int main() {
    float weight, height, bmi;

    printf("Enter your weight in kilograms: ");
    scanf("%f", &weight);

    printf("Enter your height in meters: ");
    scanf("%f", &height);

   
    bmi = weight / (height * height);

 
    printf("Your BMI is: %.2f\n", bmi);

  
    if (bmi < 18.5) {
        printf("underweight.\n");
    } else if (bmi >= 18.5 && bmi < 25.0) {
        printf("healthy weight\n");
    } else if (bmi >= 25.0 && bmi < 30.0) {
        printf("overweight \n");
    } else {
        printf("obesity range.\n");
    }

  
}

#include <stdio.h>
int main(){

float number1;
float number2;
char operation;//+
printf("Enter number 1 :");
scanf("%f",&number1);
printf("Tell Which operation you want  :");
scanf(" %c",&operation); //-+*/
printf("Enter number 2 :");
scanf("%f",&number2);


switch (operation)
{
    case '+' :   //operation=='+'
    printf("\n Sum :%f",number1+number2);
    break;


    case '-' :
    printf("\n subtruction : %f",number1-number2);
    break;

    case '*' :
    printf("\n multiplication : %f",number1*number2);
    break;
    case '/' :
    printf("\nDivision : %f",number1/number2);
    break;

default:
    break;
}







}

#include <stdio.h>
int main(){

    // a
    // +
    // b
    // ans:a+b

    // 10
    // +
    // 10
    // result=20

float number1;
float number2;
char operation;

scanf("%f",&number1);
scanf(" %c",&operation);
scanf("%f",&number2);


switch (operation)
{
case '+' :
    printf("\n Sum :%f",number1+number2);
    break;

    case '-' :
    printf("\n subtruction : %f",number1-number2);
    break;


default:
    break;
}

}

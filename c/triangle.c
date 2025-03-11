#include <stdio.h>
int main(){

    float a,b,c;
    scanf("%f",&a);
    scanf("%f",&b);
    scanf("%f",&c);

    float result=a+b+c;

    if(result == 180){
        printf("yes its triangle");
    }
    else{
        printf("its not triengle");
    }

}
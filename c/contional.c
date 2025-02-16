#include <stdio.h>
int main(){

int data=10;
//only if condition
if(data>1){
    printf("%d is greater then 1\n",data);
}
//here if else
int age2=10;
int age=10;
if(age>=18){
	printf("You are able to vote cause your age is %d\n",age);
}
else{
	age2=20;
	printf("you are not eligible to vote\n");
	
}
printf("age 2=%d ",age2);//20


//else if

if(0>11245){
	printf("10 greater");
}
else if(0>1){
	printf("5 is greater");
	
}
else{
	printf("else working");
}
}

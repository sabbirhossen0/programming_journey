#include <stdio.h>
//global variable 
int number=20;
int main(){

printf("hello");

//single line comment 
printf("single line comment \n");// comment 

/*multi 
line 
 comment */
printf("multi line comment\n ");

// this is locul variable 
int a =10;
 a =5;//value change
printf("%d\n",a);


//global variable display 
printf("%d",number );

//multi variable 
int b=10,c=20,d =30;
printf("\nb=%d, c= %d, d=%d\n",b,c,d);

//memory size 
int id=10;
char name='r';
float cgpa=3.95;
double due=1500321;

printf("%lu\n",sizeof(id));
printf("%lu\n",sizeof(name));
printf("%lu\n",sizeof(cgpa));
printf("%lu\n",sizeof(due));


//constant  number 

const float pi=3.1416;

// pi=2.4154;   when we run then show error 
printf("Constant number :%.2f",pi);


//operator +-*/%
printf("\noperator\n");

int x=5+5;
int y=20-10;
int z=5*2;
int w=20/2;
int q=30%2;

printf("\naddition=%d subtruction=%d multiplication=%d divition=%d  modulas=%d",x,y,z,w,q);
}
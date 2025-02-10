#include <stdio.h>
int main(){
  //assaignment operator 

int a=10;
a+=10;  //a=a+10
int b=20;
b-=10;  //b=b-10

int c=10;
c*=3;

int d=40;
d/=4; //d=d/4;  d=40/4=10

printf("%d\n%d\n%d\n%d\n",a,b,c,d);


// 1&1=1
// 0&1=0
// 1&0=0
// 0&0=0

// 8421
// 0011=3
// 0101=5
int x = 5;  //5=0101
//3=0011
x |= 3;
printf("%d", x);






}
#include <stdio.h>
int main (){

    // Write a program to check if a number is positive, negative, or zero.
    int number;
    printf("\nEnter a integer number :");  //leveling
    scanf("%d",&number);

//     if(number>0){//
//         printf("%d is positive number",number);
//     }
//     else if(number<0){
// printf("%d is a nagetive number ",number);
//     }
//     else{
//         printf("%d is a Zero",number);
//     }
  
if(number>0){//
    printf("%d is positive number",number);
}
if(number<0){
printf("%d is a nagetive number ",number);
}
if (number ==0)
{
    printf("%d is a Zero",number);
}	   
}

#include <stdio.h>
//Write a program to check if a number is 10,20,30,40 show "yes"" ?
int main(){

    int number; //declear 
    printf("Enter A Number :");
    scanf("%d",&number);


//     if (number==10)
//     {
//         printf("yes");
//     }
//    else if(number==20)
//     {
//         printf("\nyes");
//     }
//     else if(number==30)
//     {
//         printf("\nyes");
//     }
//     else if(number==40)
//     {
//         printf("\nyes");
//     }
// else{
//     printf("no");
// }


if(number==10 || number==20 || number==30 || number==40 )
{
printf("yes");

}
else {
    printf("no");
}
}
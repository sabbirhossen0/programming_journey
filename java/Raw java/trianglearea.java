import java.util.Scanner;



class circle{
float R0,R1;

void setredius(float a,float b){
R0=a;
R1=b;
}

void printArea(){
    float r0a= (float) (3.14*R0*R0) ;
    float r1a=(float) (3.14*R1*R1);
float sub=r1a-r0a;
System.out.println(sub);

}

}
public class trianglearea{


    public static void main(String[]  args){

circle c1=new circle();

        Scanner scanner=new Scanner(System.in);

System.out.print("Enter redius R0 : ");
float r0=scanner.nextFloat();

System.out.print("Enter redius R1 : ");
float r1=scanner.nextFloat();

c1.setredius(r0, r1);
c1.printArea();
    }


} 
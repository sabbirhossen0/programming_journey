class student{
String name;
int id;
String section;
    student(){
name="sasbbir";
id =499;
section="c";
    }
}
public class classandobj{
public static void main(String[] args){
System.out.println(" hello java\n");
student ob=new student();
int id1=ob.id;
System.out.println(id1);
}

}
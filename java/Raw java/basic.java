class animal{
animal(){
    System.out.println("animal class created ");
}
void fly(){
    System.out.println("yes amnimal flyiing ?");
}
void eat(){
    System.out.println("yes amnimal eating !");
}
}

class bird extends  animal{
    bird(){
        System.out.println("bird  class created ");
    }
    void fly(){
        System.out.println("yes bird flyiing ?");
    }
    }


class eagle extends bird{
    eagle(){
System.out.println("Eagle class created");
    }
    void fly(int speed){
        System.out.println("yes bird flyiing speed is  ?"+speed);
    }
    void eat(){
        System.out.println("yes eagle eating !");
    }
}

public class basic{

    public static void main(String[] args){
        System.out.println("hello this is sabbir hossen ");
        animal a=new animal();
        bird b=new bird();
        eagle e=new eagle();
a.fly();
b.fly();
e.fly(30);
a.eat();
b.eat();
e.eat();

    }


}
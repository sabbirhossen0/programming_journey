
class fruitx {
    String name;
 float Weight;
 float ppk;
 float tp;
fruitx(String n, float w,float p){
    name=n;
    ppk=p;
    Weight=w;
}

void increasePricePerKG(int r){ppk+=r;}
void reducePricePerKG(int r){ppk-=r;}
void printDetails(){
    tp=ppk*Weight;
System.out.println("name :"+name);
System.out.println("Weight :"+Weight);
System.out.println("price per kg :"+ppk);
System.out.println("Total Price :"+tp);

}
  
}

public class fruit {

public static void main (String[] args){
    // fruitx fruit1=new fruitx("apple",3.5,);
    fruitx fruit1=new fruitx("Apple", 3.5f, 110);
    fruitx fruit2=new fruitx("Mango", 5f, 90);
    fruit1.reducePricePerKG(10);
        fruit2.increasePricePerKG(10);
        fruit1.printDetails();
        fruit2.printDetails();
} 
}

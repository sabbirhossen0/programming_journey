#include <iostream>
using namespace std;
class Course{
    public:
    string name, id;
    int credit;
    Course(string a, string b, int c){
    name = a;
    id = b;
    credit = c;
    }
    void display(){
    cout << name <<"\n"<<id <<"\n"<< credit <<"\n";
    }
    void updateCourse(Course c){
    name = c.name;
    id = c.id;
    credit = c.credit--;
    }
    
    };

int main(){
    Course c1("math","math21", 3);
    Course c2("english","english 21", 3);
    c1.display();
    c2.display();
    c1.updateCourse(c2);
    c1.display();
    c2.display();
    return 0;
    }
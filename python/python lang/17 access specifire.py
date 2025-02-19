class MyClass:
    def __init__(self):
        self.public_var = "I am Public"

    def public_method(self):
        return "Public Method"

obj = MyClass()
print(obj.public_var)  # Accessible
print(obj.public_method())  # Accessible


class MyClass:
    def __init__(self):
        self._protected_var = "I am Protected"

    def _protected_method(self):
        return "Protected Method"

obj = MyClass()
print(obj._protected_var)  # Accessible, but should be used with caution
print(obj._protected_method())  # Accessible, but should be used with caution





class MyClass:
    def __init__(self):
        self.__private_var = "I am Private"

    def __private_method(self):
        return "Private Method"

obj = MyClass()
# print(obj.__private_var)  # AttributeError: 'MyClass' object has no attribute '__private_var'
# print(obj.__private_method())  # AttributeError

# Access using name mangling
print(obj._MyClass__private_var)  # Works but not recommended
print(obj._MyClass__private_method())  # Works but not recommended

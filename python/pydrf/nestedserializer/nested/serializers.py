from .models import book, author
from rest_framework import serializers



class bookSerializer(serializers.ModelSerializer):
    class meta:
        model:book
        field:('__all__')


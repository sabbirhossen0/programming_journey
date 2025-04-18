# contact_app/views.py
from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.core.mail import send_mail
from .models import Contact
from .serializers import ContactSerializer

@api_view(['POST'])
def contact_us(request):
    serializer = ContactSerializer(data=request.data)
    if serializer.is_valid():
        serializer.save()

        # Send email to you
        subject = "New Contact Form Submission"
        message = f"""
        Name: {request.data.get('first_name')} {request.data.get('last_name')}
        Email: {request.data.get('email')}
        Message: {request.data.get('message')}
        """
        send_mail(
            subject,
            message,
            'wonderfully701@gmail.com',  # Change to your Gmail
            ['sabbirnubcse@gmail.com'],    # Your email where the message goes
        )

        return Response({"status": "Message sent and saved successfully"})
    return Response(serializer.errors)

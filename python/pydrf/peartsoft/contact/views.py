from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.core.mail import send_mail, BadHeaderError
from .models import contact
from .serializers import ContactSerializer

@api_view(['POST'])
def contact_us(request):
    serializer = ContactSerializer(data=request.data)
    if serializer.is_valid():
        serializer.save()

        first_name = serializer.validated_data.get('first_name')
        last_name = serializer.validated_data.get('last_name')
        email = serializer.validated_data.get('email')
        message = serializer.validated_data.get('message')

        subject = "New Contact Form Submission"
        body = f"""
        Name: {first_name} {last_name}
        Email: {email}
        Message: {message}
        """

        try:
            send_mail(
                subject,
                body,
                'wonderfully701@gmail.com',  # Your Gmail (must be configured)
                ['sabbir472003@gmail.com'],   # Recipient
                fail_silently=False,
            )

            send_mail(
                "PearT Soft - Thanks for Contacting Us!",
                "Hi there,\n\nThanks for contacting us. We'll get back to you shortly.\n\nBest,\nPearT Soft Team",
                'wonderfully701@gmail.com',  # Your Gmail (must be configured)
                [email],   # Recipient
                fail_silently=False,
            )




            return Response({"status": "Message sent and saved successfully"})
        except BadHeaderError:
            return Response({"error": "Invalid header found."}, status=400)
        except Exception as e:
            return Response({"error": f"Failed to send email: {str(e)}"}, status=500)

    return Response(serializer.errors, status=400)

import google.generativeai as genai
from django.conf import settings
from rest_framework.decorators import api_view
from rest_framework.response import Response

# Configure Gemini
genai.configure(api_key=settings.GEMINIAI_API_KEY)
model = genai.GenerativeModel('models/gemini-2.0-flash')

# models = genai.list_models()
# for m in models:
#     print(m.name)



@api_view(['POST'])
def chat_with_gamini(request):
    user_message = request.data.get('message')

    if not user_message:
        return Response({'error': 'Message is required.'}, status=400)

    # Customize prompt for motivational ayah
    prompt = (
        f"The user is feeling sad and shares: '{user_message}'. "
        "Please reply with a motivational and comforting Surah or Ayah from the Quran with arabic and  a simple explanation in  English."
    )

    try:
        response = model.generate_content(prompt)
        return Response({'reply': response.text})
    except Exception as e:
        return Response({'error': str(e)}, status=500)

import openai
from django.conf import settings
from rest_framework.decorators import api_view
from rest_framework.response import Response

openai.api_key = settings.OPENAI_API_KEY

@api_view(['POST'])
def chat_with_bot(request):
    user_message = request.data.get("message", "")

    prompt = f"""
The user is feeling sad or depressed. Their message: "{user_message}"
You are a caring Islamic assistant. Respond with a short, motivational Surah Ayat in Arabic, its simple translation in English, and a few words of encouragement.
"""

    try:
        chat_response = openai.ChatCompletion.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "You are an Islamic chatbot that provides motivational Quranic Ayat."},
                {"role": "user", "content": prompt}
            ]
        )
        reply = chat_response.choices[0].message['content']
        return Response({"reply": reply})
    except Exception as e:
        return Response({"error": str(e)}, status=500)

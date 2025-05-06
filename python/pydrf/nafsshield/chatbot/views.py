from openai import OpenAI
from django.conf import settings
from rest_framework.decorators import api_view
from rest_framework.response import Response

client = OpenAI(api_key=settings.OPENAI_API_KEY)

# models = OpenAI.list_models()
# for m in models:
#     print(m.name)


@api_view(['POST'])
def chat_with_bot(request):
    user_message = request.data.get("message", "")

    prompt = f"""
The user is feeling sad or depressed. Their message: "{user_message}"
Explain in Bangla language
You are a caring Islamic assistant. Respond with a short, motivational Surah Ayat in Arabic, বাংলা ভাষায় এর সহজ অনুবাদ,, and a few words of encouragement.
"""

    try:
        response = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[
                {"role": "system", "content": "You are an Islamic chatbot that provides motivational Quranic Ayat."},
                {"role": "user", "content": prompt}
            ]
        )
        reply = response.choices[0].message.content
        return Response({"reply": reply})
    except Exception as e:
        return Response({"error": str(e)}, status=500)

from rest_framework.pagination import PageNumberPagination
from rest_framework.response import Response
from rest_framework.decorators import api_view
from .models import Item
from .serializers import ItemSerializer

@api_view(['GET'])
def paginated_items(request):
    queryset = Item.objects.all().order_by('id')
    paginator = PageNumberPagination()
    paginator.page_size = 10  # Items per page
    result_page = paginator.paginate_queryset(queryset, request)
    return paginator.get_paginated_response(ItemSerializer(result_page, many=True).data)

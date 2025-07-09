thisdist={
"name":"sabbir",
"age":"21",
"cgpa":"3.14"
}


print(thisdist)

print(thisdist["age"])




users = [
    {"id": 1, "name": "Alice", "roles": ["admin", "editor"]},
    {"id": 2, "name": "Bob", "roles": ["viewer"]},
    {"id": 3, "name": "Charlie", "roles": ["editor", "viewer"]}
]

for user in users:
    print(user["name"], "roles:", ", ".join(user["roles"]))


import heapq

class State:
    def __init__(self, node, g, f, parent=None):
        self.node = node
        self.g = g
        self.f = f
        self.parent = parent

    def __lt__(self, other):
        return self.f < other.f


def astar(V, edges, heuristics, start=0, goal=None):
    # Build adjacency list
    graph = {i: [] for i in range(V)}
    for u, v, w in edges:
        graph[u].append((v, w))
        graph[v].append((u, w))  # undirected

    if goal is None:
        goal = V - 1  # default last node as goal

    # Priority queue
    Q = []
    start_state = State(start, 0, heuristics[start])
    heapq.heappush(Q, start_state)

    visited = {}

    while Q:
        curr = heapq.heappop(Q)

        # If goal found → reconstruct path
        if curr.node == goal:
            path = []
            total_cost = curr.g
            while curr:
                path.append(curr.node)
                curr = curr.parent
            return path[::-1], total_cost

        if curr.node in visited and visited[curr.node] <= curr.g:
            continue
        visited[curr.node] = curr.g

        # Expand neighbors
        for neighbor, cost in graph[curr.node]:
            g_new = curr.g + cost
            h_new = heuristics[neighbor]
            f_new = g_new + h_new
            new_state = State(neighbor, g_new, f_new, curr)
            heapq.heappush(Q, new_state)

    return None, float("inf")


# -------------------------
# Sample Input
V, E = 7, 9
edges = [
    (0, 1, 4),
    (0, 2, 3),
    (1, 4, 12),
    (1, 5, 5),
    (2, 3, 7),
    (2, 4, 10),
    (3, 4, 2),
    (4, 6, 5),
    (5, 6, 16)
]
heuristics = [14, 12, 11, 6, 4, 11, 0]

# Run A*
path, cost = astar(V, edges, heuristics, start=0, goal=6)

# Output
print("Path:", " --> ".join(map(str, path)))
print("Total Actual Cost:", cost)

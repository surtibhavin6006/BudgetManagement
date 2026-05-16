import json
import redis.asyncio as aioredis
from app.config import settings

_pool: aioredis.ConnectionPool | None = None


def get_pool() -> aioredis.ConnectionPool:
    global _pool
    if _pool is None:
        _pool = aioredis.ConnectionPool.from_url(
            f"redis://{settings.redis_host}:{settings.redis_port}"
        )
    return _pool


async def publish_statement_uploaded(statement_id: int, user_id: int) -> None:
    client = aioredis.Redis(connection_pool=get_pool())
    payload = json.dumps({
        "event":        "statement.uploaded",
        "statement_id": statement_id,
        "user_id":      user_id,
    })
    await client.publish("statements:events", payload)

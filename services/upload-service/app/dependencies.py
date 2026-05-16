from functools import lru_cache

from app.services.minio_client import MinioStorage
from app.services.storage import StorageInterface


@lru_cache
def get_storage() -> StorageInterface:
    return MinioStorage()

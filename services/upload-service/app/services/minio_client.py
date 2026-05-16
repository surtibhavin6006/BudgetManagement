import io

from minio import Minio

from app.config import settings
from app.services.storage import StorageInterface


class MinioStorage(StorageInterface):

    def __init__(self) -> None:
        self._client = Minio(
            f"{settings.minio_host}:{settings.minio_port}",
            access_key=settings.minio_access_key,
            secret_key=settings.minio_secret_key,
            secure=False,
        )
        self._bucket = settings.minio_bucket

    def ensure_bucket(self) -> None:
        if not self._client.bucket_exists(self._bucket):
            self._client.make_bucket(self._bucket)

    def upload(self, path: str, data: bytes, content_type: str) -> str:
        self._client.put_object(
            self._bucket,
            path,
            io.BytesIO(data),
            length=len(data),
            content_type=content_type,
        )
        return path

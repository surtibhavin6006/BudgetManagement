from abc import ABC, abstractmethod


class StorageInterface(ABC):

    @abstractmethod
    def ensure_bucket(self) -> None: ...

    @abstractmethod
    def upload(self, path: str, data: bytes, content_type: str) -> str: ...

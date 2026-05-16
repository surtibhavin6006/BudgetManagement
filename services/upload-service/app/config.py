from pydantic_settings import BaseSettings


class Settings(BaseSettings):
    db_host: str = "mysql"
    db_port: int = 3306
    db_database: str = "budget_management"
    db_username: str = "app"
    db_password: str = "secret"

    redis_host: str = "redis"
    redis_port: int = 6379

    minio_host: str = "minio"
    minio_port: int = 9000
    minio_access_key: str = "minioadmin"
    minio_secret_key: str = "minioadmin"
    minio_bucket: str = "statements"

    class Config:
        env_file = ".env"


settings = Settings()

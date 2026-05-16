from sqlalchemy.ext.asyncio import AsyncSession, create_async_engine
from sqlalchemy.orm import sessionmaker
from app.config import settings

DATABASE_URL = (
    f"mysql+aiomysql://{settings.db_username}:{settings.db_password}"
    f"@{settings.db_host}:{settings.db_port}/{settings.db_database}"
)

engine = create_async_engine(DATABASE_URL, pool_pre_ping=True)

AsyncSessionLocal = sessionmaker(
    engine,
    class_=AsyncSession,
    expire_on_commit=False,
)


async def get_db():
    async with AsyncSessionLocal() as session:
        yield session

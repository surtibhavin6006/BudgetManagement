from contextlib import asynccontextmanager

from fastapi import FastAPI

from app.dependencies import get_storage
from app.routers import upload


@asynccontextmanager
async def lifespan(app: FastAPI):
    get_storage().ensure_bucket()
    yield


app = FastAPI(title="Upload Service", lifespan=lifespan)

app.include_router(upload.router)

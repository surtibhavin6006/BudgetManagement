import uuid

from fastapi import APIRouter, Depends, File, Header, HTTPException, UploadFile
from sqlalchemy import text
from sqlalchemy.ext.asyncio import AsyncSession

from app.database import get_db
from app.dependencies import get_storage
from app.services.redis_publisher import publish_statement_uploaded
from app.services.storage import StorageInterface

router = APIRouter()

ALLOWED_CONTENT_TYPES = {"application/pdf"}


@router.post("/statement", status_code=201)
async def upload_statement(
    file: UploadFile = File(...),
    x_user_id: int = Header(...),
    db: AsyncSession = Depends(get_db),
    storage: StorageInterface = Depends(get_storage),
):
    if file.content_type not in ALLOWED_CONTENT_TYPES:
        raise HTTPException(status_code=400, detail="Only PDF files are accepted.")

    data = await file.read()
    if not data:
        raise HTTPException(status_code=400, detail="Uploaded file is empty.")

    object_path = f"users/{x_user_id}/{uuid.uuid4()}.pdf"
    storage.upload(object_path, data, file.content_type)

    result = await db.execute(
        text("""
            INSERT INTO statements (user_id, file_path, original_filename, status, created_at, updated_at)
            VALUES (:user_id, :file_path, :original_filename, 'uploaded', NOW(), NOW())
        """),
        {
            "user_id":           x_user_id,
            "file_path":         object_path,
            "original_filename": file.filename,
        },
    )
    await db.commit()

    statement_id = result.lastrowid

    await publish_statement_uploaded(statement_id, x_user_id)

    return {"statement_id": statement_id}

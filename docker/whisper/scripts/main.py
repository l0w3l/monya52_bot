import os
import tempfile
import whisper
from fastapi import FastAPI, UploadFile, File
from fastapi.responses import JSONResponse

MODEL_NAME = os.getenv("WHISPER_MODEL", "small")

app = FastAPI(title="Whisper API")

model = whisper.load_model(MODEL_NAME)


@app.post("/transcribe")
async def transcribe(file: UploadFile = File(...)):
    # сохраняем во временный файл
    with tempfile.NamedTemporaryFile(delete=False, suffix=file.filename) as tmp:
        tmp.write(await file.read())
        tmp_path = tmp.name

    try:
        result = model.transcribe(tmp_path)
        return JSONResponse({
            "text": result["text"],
            "language": result["language"],
            "segments": result["segments"],
        })
    finally:
        os.remove(tmp_path)

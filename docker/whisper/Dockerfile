FROM python:3.10-slim

RUN apt-get update && apt-get install -y \
    ffmpeg \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN pip install --no-cache-dir \
    openai-whisper \
    python-multipart \
    fastapi \
    uvicorn[standard]

WORKDIR /app

COPY scripts /app

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]

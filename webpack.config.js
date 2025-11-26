{
  "output": {
    "filename": "[name].js",
    "path": "build"
  },
  "module": {
    "rules": [
      {
        "test": "/\\.scss$/",
        "use": [
          "sass-loader"
        ]
      }
    ]
  },
  "customScriptProperties": {
    "source": "src",
    "output": "build"
  }
}

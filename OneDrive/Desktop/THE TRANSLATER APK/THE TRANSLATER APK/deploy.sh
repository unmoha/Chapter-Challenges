#!/bin/bash

echo "Building Translator Desktop for distribution..."

echo ""
echo "1. Cleaning and compiling..."
mvn clean compile

echo ""
echo "2. Creating executable JAR with dependencies..."
mvn package

echo ""
echo "3. Creating distribution packages..."
mvn jpackage:jpackage

echo ""
echo "Build complete!"
echo ""
echo "Distribution files are in:"
echo "- ui-fx/target/ui-fx-0.1.0-jar-with-dependencies.jar (portable JAR)"
echo "- ui-fx/target/dist/ (native installers)"
echo ""
echo "To run the portable version:"
echo "java -jar ui-fx/target/ui-fx-0.1.0-jar-with-dependencies.jar"
echo ""

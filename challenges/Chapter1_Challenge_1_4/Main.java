import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;

public class Main {
    public static void main(String[] args) {
        try (BufferedReader br = new BufferedReader(new FileReader("config.txt"))) {
            // Read and parse version
            int version = Integer.parseInt(br.readLine());
            if (version < 2) {
                throw new Exception("Config version too old!");
            }

            // Read file path and check existence
            String path = br.readLine();
            if (!new File(path).exists()) {
                throw new IOException("Configured file does not exist!");
            }

            System.out.println("Config loaded successfully.");
        } catch (FileNotFoundException e) {
            System.out.println("Error: Config file not found.");
        } catch (NumberFormatException e) {
            System.out.println("Error: Invalid version number.");
        } catch (IOException e) {
            System.out.println("Error: " + e.getMessage());
        } catch (Exception e) {
            System.out.println("Error: " + e.getMessage());
        } finally {
            System.out.println("Config read attempt finished.");
        }
    }
}
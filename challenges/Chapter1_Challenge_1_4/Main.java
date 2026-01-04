import java.io.*;
import java.io.File;

public class Main {
    public static void main(String[] args) {
        try (BufferedReader br = new BufferedReader(new FileReader("config.txt"))) {
            int version = Integer.parseInt(br.readLine());
            if (version < 2) {
                throw new Exception("Config version too old!");
            }

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
import java.io.*;
import java.nio.file.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Scanner;

public class Main {
    static Path dir = Paths.get("entries");

    public static void main(String[] args) throws IOException {
        if (!Files.exists(dir)) Files.createDirectory(dir);
        Scanner sc = new Scanner(System.in);

        System.out.println("1. Write Entry  2. Read Entries");
        int choice = sc.nextInt(); sc.nextLine();

        if (choice == 1) {
            System.out.println("Write entry:");
            String text = sc.nextLine();
            String name = "diary_" +
                LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy_MM_dd_HH_mm_ss")) + ".txt";
            Files.write(dir.resolve(name), text.getBytes());
        } else {
            Files.list(dir).forEach(System.out::println);
        }
        sc.close();
    }
}
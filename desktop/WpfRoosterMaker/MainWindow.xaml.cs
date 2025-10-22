using MySql.Data.MySqlClient;
using Mysqlx.Crud;
using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace WpfRoosterMaker
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            LoadAgenda();
        }

        public static DataTable dt_klassen = new DataTable();
        public static ListBoxItem SelectedListBox = new ListBoxItem();
        public static DateTime SelectedWeek = new DateTime();
        public static string SelectedKlas = "";

        List<ListBox> lists = new List<ListBox>();

        public static MySqlConnection Connect()
        {
            string connectionstring = "SERVER=localhost;DATABASE=DBAgenda;UID=root;PASSWORD=";
            try
            {
                MySqlConnection cnct = new MySqlConnection(connectionstring);
                return cnct;
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.ToString());
                return null;
            }

        }


        private void btnBackward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(-1);
        }

        private void btnForward_Click(object sender, RoutedEventArgs e)
        {
            ChangeDate(1);
        }

        private void LoadAgenda()
        {
            dpWeek.SelectedDate = DateTime.Now;
            lists.Add(lb1);
            lists.Add(lb2);
            lists.Add(lb3);
            lists.Add(lb4);
            lists.Add(lb5);

            MySqlConnection connection = Connect();
            if (connection == null) return;

            // Some deployments don't have a separate 'klassen' table. Use DISTINCT klas from `schedule` so the app works with the PHP schema.
            MySqlCommand cmd = new MySqlCommand("SELECT DISTINCT `klas` FROM `schedule` ORDER BY `klas` ASC", connection);
            try
            {
                connection.Open();
                dt_klassen.Load(cmd.ExecuteReader());
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error loading classes: " + ex.Message);
            }
            finally
            {
                try { connection.Close(); } catch { }
            }

            foreach (DataRow row in MainWindow.dt_klassen.Rows)
            {
                var value = row["klas"];
                if (value != null && value != DBNull.Value)
                    cbKlas.Items.Add(value.ToString());
            }
            if (cbKlas.Items.Count > 0)
                cbKlas.SelectedIndex = 0;
        }

        private void ChangeDate(int index)
        {
            if (dpWeek.SelectedDate.HasValue)
                dpWeek.SelectedDate = dpWeek.SelectedDate.Value.AddDays(index * 7);
            else
                dpWeek.SelectedDate = DateTime.Now.AddDays(index * 7);
        }

        private void dpWeek_SelectedDateChanged(object sender, SelectionChangedEventArgs e)
        {
            // Make this robust: use dpWeek.SelectedDate if available
            DateTime newDate = dpWeek.SelectedDate ?? DateTime.Now;

            while (newDate.DayOfWeek != dpWeek.FirstDayOfWeek)
                newDate = newDate.AddDays(-1);

            dpWeek.SelectedDate = newDate;
            SelectedWeek = dpWeek.SelectedDate.Value;
            Read();
        }

        private void btnCreate_Click(object sender, RoutedEventArgs e)
        {
            CreateWindow createwindow = new CreateWindow();
            createwindow.Owner = this;
            createwindow.ShowDialog();
            Read();
        }

        public static void Create(string subject, string klas, string scheduleDate, string teacher, string room, string beginTime, string endTime)
        {
            MySqlConnection connection = Connect();
            if (connection == null) return;

            MySqlCommand cmd = new MySqlCommand(@"INSERT INTO `schedule` (klas, schedule_date, subject, teacher, room, begin_time, end_time) VALUES (@klas, @schedule_date, @subject, @teacher, @room, @begin_time, @end_time)", connection);
            cmd.Parameters.AddWithValue("@subject", subject ?? string.Empty);
            cmd.Parameters.AddWithValue("@klas", klas ?? string.Empty);
            if (!int.TryParse(scheduleDate, out int scheduleDateValue)) scheduleDateValue = 0;
            cmd.Parameters.AddWithValue("@schedule_date", scheduleDateValue);
            cmd.Parameters.AddWithValue("@teacher", teacher ?? string.Empty);
            cmd.Parameters.AddWithValue("@room", room ?? string.Empty);
            cmd.Parameters.AddWithValue("@begin_time", beginTime ?? string.Empty);
            cmd.Parameters.AddWithValue("@end_time", endTime ?? string.Empty);
            try
            {
                connection.Open();
                cmd.ExecuteNonQuery();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error inserting schedule: " + ex.Message);
            }
            finally
            {
                try { connection.Close(); } catch { }
            }
        }

        private void Read()
        {
            MySqlConnection connection = Connect();
            if (connection == null) return;

            if (cbKlas.SelectedItem == null) return;

            for (int i = 0; i < lists.Count; i++)
            {
                lists[i].Items.Clear();
                DataTable dt_rooster = new DataTable();
                using (MySqlCommand cmd = new MySqlCommand("SELECT `id`,`klas`,`schedule_date`,`subject`,`teacher`,`room`,`begin_time`,`end_time` FROM `schedule` WHERE `schedule_date`=@schedule_date AND `klas`=@klas ORDER BY `begin_time` ASC", connection))
                {
                    DateTime baseDay = dpWeek.SelectedDate ?? DateTime.Now;
                    DateTime day = baseDay.AddDays(i);
                    ListBoxItem item = new ListBoxItem();
                    item.Focusable = false;
                    item.Content = day.Date.ToString("yyyy/MM/dd");
                    Style style = this.FindResource("Header") as Style;
                    item.Style = style;
                    item.Tag = "Header";
                    lists[i].Items.Add(item);

                    string scheduleDateInt = day.ToString("yyyyMMdd");
                    if (!int.TryParse(scheduleDateInt, out int scheduleDateValue)) scheduleDateValue = 0;
                    cmd.Parameters.AddWithValue("@schedule_date", scheduleDateValue);
                    cmd.Parameters.AddWithValue("@klas", cbKlas.SelectedItem.ToString());
                    try
                    {
                        connection.Open();
                        dt_rooster.Load(cmd.ExecuteReader());
                    }
                    catch (Exception ex)
                    {
                        MessageBox.Show("Error reading schedule: " + ex.Message);
                    }
                    finally
                    {
                        try { connection.Close(); } catch { }
                    }
                }

                foreach (DataRow row in dt_rooster.Rows)
                {
                    string begintijd = row["begin_time"].ToString();
                    if (begintijd.Length == 4) begintijd = begintijd.Insert(2, ":");
                    string eindtijd = row["end_time"].ToString();
                    if (eindtijd.Length == 4) eindtijd = eindtijd.Insert(2, ":");
                    ListBoxItem listBoxItem = new ListBoxItem();
                    listBoxItem.Tag = row["id"];
                    string subject = row["subject"].ToString();
                    string klas = row["klas"].ToString();
                    string teacher = row.Table.Columns.Contains("teacher") ? row["teacher"].ToString() : string.Empty;
                    string room = row.Table.Columns.Contains("room") ? row["room"].ToString() : string.Empty;
                    listBoxItem.Content = $"{begintijd}-{eindtijd}\n{subject}\n{klas}\n{teacher}\n{room}";
                    lists[i].Items.Add(listBoxItem);
                }
            }

        }

        public static void Update(string subject, string klas, string scheduleDate, string teacher, string room, string beginTime, string endTime)
        {
            MySqlConnection connection = Connect();
            if (connection == null) return;
            if (MainWindow.SelectedListBox == null || MainWindow.SelectedListBox.Tag == null) return;

            MySqlCommand cmd = new MySqlCommand(@"UPDATE `schedule` SET klas = @klas, schedule_date = @schedule_date, teacher = @teacher, room = @room, subject = @subject, begin_time = @begin_time, end_time = @end_time WHERE `id`=@id", connection);
            cmd.Parameters.AddWithValue("@subject", subject ?? string.Empty);
            cmd.Parameters.AddWithValue("@klas", klas ?? string.Empty);
            if (!int.TryParse(scheduleDate, out int scheduleDateValue2)) scheduleDateValue2 = 0;
            cmd.Parameters.AddWithValue("@schedule_date", scheduleDateValue2);
            cmd.Parameters.AddWithValue("@teacher", teacher ?? string.Empty);
            cmd.Parameters.AddWithValue("@room", room ?? string.Empty);
            cmd.Parameters.AddWithValue("@begin_time", beginTime ?? string.Empty);
            cmd.Parameters.AddWithValue("@end_time", endTime ?? string.Empty);
            cmd.Parameters.AddWithValue("@id", MainWindow.SelectedListBox.Tag);
            try
            {
                connection.Open();
                cmd.ExecuteNonQuery();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error updating schedule: " + ex.Message);
            }
            finally
            {
                try { connection.Close(); } catch { }
            }

        }

        public static void Delete(string id)
        {
            MySqlConnection connection = Connect();
            if (connection == null) return;

            MySqlCommand cmd = new MySqlCommand(@"DELETE FROM `schedule` WHERE id = @id", connection);
            cmd.Parameters.AddWithValue("@id", id);
            try
            {
                connection.Open();
                cmd.ExecuteNonQuery();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error deleting schedule: " + ex.Message);
            }
            finally
            {
                try { connection.Close(); } catch { }
            }
        }

        private void SelectionChanged(object sender, RoutedEventArgs e)
        {
            ListBox listbox = sender as ListBox;
            ListBoxItem item = listbox.SelectedItem as ListBoxItem;
            if (item != null)
            {
                SelectedListBox = (ListBoxItem)listbox.SelectedItem;
                UpdateWindow updatewindow = new UpdateWindow();
                updatewindow.ShowDialog();
                Read();
                listbox.SelectedItem = null;
            }
        }



        private void cbKlas_SelectionChanged(object sender, SelectionChangedEventArgs e)
        {
            SelectedKlas = (sender as ComboBox).SelectedItem.ToString();
            Read();
        }
    }
}
